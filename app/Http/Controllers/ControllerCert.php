<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use setasign\Fpdi\Fpdi;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel as LaravelExcel;    
use App\Imports\CertificadosImport;
use Illuminate\Support\Facades\Log;
use App\Models\Certificado;
use Carbon\Carbon;
use App\Models\EmissaoCertificadoArquivo;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificadoEnviado;
use Illuminate\Support\Facades\Validator;


class ControllerCert extends Controller
{
    public function gerarCertificados(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls',
        'template' => 'required|string',
        'mapa' => 'required|string', // JSON vindo do front
        'enviar_email' => 'sometimes|string'
    ]);

    $certificadosGerados = [];
    $erros = [];
    $quantidadeCertificados = 0;

    try {
        $mapa = json_decode($request->input('mapa'), true); 
        $dados = LaravelExcel::toArray(new CertificadosImport, $request->file('file'));

        if (empty($dados) || empty($dados[0])) {
            return response()->json(['erro' => 'O arquivo Excel está vazio ou mal formatado.'], 400);
        }

        $templatePath = storage_path("app/templates/" . basename($request->template));
        if (!file_exists($templatePath)) {
            return response()->json(['erro' => 'O template do certificado não foi encontrado.'], 400);
        }

        foreach (array_slice($dados[0], 1) as $index => $linha) {
            if (empty(array_filter($linha))) continue;

            // 🔑 Dados baseados no mapeamento
            $nome         = trim($linha[$mapa['nome']] ?? '');
            $curso        = trim($linha[$mapa['curso']] ?? '');
            $email        = trim($linha[$mapa['email']] ?? '');
            $cargaHoraria = trim($linha[$mapa['carga_horaria']] ?? '');
            $dataConclusao = trim($linha[$mapa['data_conclusao']] ?? '');
            $unidade      = trim($linha[$mapa['unidade']] ?? '');
            $cpf          = trim($linha[$mapa['cpf']] ?? '');

            // ✅ Validações
            if (empty($nome) || empty($curso) || empty($cargaHoraria) || empty($dataConclusao) || empty($unidade) || empty($cpf)) {
                $erros[] = ['linha' => $index + 2, 'nome' => $nome ?: 'N/A', 'curso' => $curso ?: 'N/A', 'erro' => 'Dados insuficientes'];
                continue;
            }

            $cpfNumerico = preg_replace('/\D/', '', $cpf);
            if (strlen($cpfNumerico) !== 11) {
                $erros[] = ['linha' => $index + 2, 'nome' => $nome, 'curso' => $curso, 'erro' => 'CPF inválido'];
                continue;
            }

            // ✅ Validação de data
            try {
                if (is_numeric($dataConclusao)) {
                    $dataConclusao = Date::excelToDateTimeObject($dataConclusao)->format('Y-m-d');
                }
                $dataConclusao = Carbon::parse($dataConclusao);
            } catch (\Exception $e) {
                $erros[] = ['linha' => $index + 2, 'nome' => $nome, 'curso' => $curso, 'erro' => 'Data inválida'];
                continue;
            }

            // ✅ Hash e QRCode
            $hash = md5($cpfNumerico . $dataConclusao);
            $qrCodeUrl = url('/verificar_certificado/' . $hash);

            // ✅ Gera PDF definitivo
            $outputPath = $this->gerarCertificadoPdf($nome, $curso, $cargaHoraria, $dataConclusao, $unidade, $qrCodeUrl, $templatePath, $hash);
            if (!$outputPath) {
                $erros[] = ['linha' => $index + 2, 'nome' => $nome, 'curso' => $curso, 'erro' => 'Erro ao gerar PDF'];
                continue;
            }

            // ✅ Salva no banco
            $certificado = Certificado::create([
                'nome' => $nome,
                'cpf' => $cpfNumerico,
                'email' => $email,
                'curso' => $curso,
                'carga_horaria' => $cargaHoraria,
                'unidade' => $unidade,
                'data_emissao' => now(),
                'data_conclusao' => $dataConclusao,
                'qr_code_path' => $qrCodeUrl,
                'certificado_path' => $outputPath,
                'hash' => $hash,
            ]);

            $certificadosGerados[] = ['nome' => $nome, 'curso' => $curso];
            $quantidadeCertificados++;

            // ✅ Envia e-mail se solicitado
            $deveEnviarEmail = in_array(strtolower($request->input('enviar_email')), ['1', 'true'], true);
            if ($deveEnviarEmail && !empty($email)) {
                $this->enviarEmailCertificado($certificado);
            }
        }

        // Log do arquivo processado
        EmissaoCertificadoArquivo::create([
            'nomeArquivo' => $request->file('file')->getClientOriginalName(),
            'qtdeCertificados' => $quantidadeCertificados,
            'status' => 'pendente',
            'dataArquivo' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'mensagem' => '✅ Certificados gerados com sucesso!',
            'quantidadeCertificados' => $quantidadeCertificados,
            'erros' => $erros,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'mensagem' => 'Erro: ' . $e->getMessage()
        ], 500);
    }
}


        private function enviarEmailCertificado(Certificado $certificado)
    {
        try {
            Mail::to($certificado->email)
                ->send(new CertificadoEnviado(
                    $certificado->nome,
                    $certificado->curso,
                    $certificado->certificado_path,
                    $certificado->hash
                ));
            
            Log::info('E-mail enviado para: ' . $certificado->email);
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail para ' . $certificado->email . ': ' . $e->getMessage());
            return false;
        }
    }

    

    private function gerarCertificadoPdf($nomeAluno, $curso, $cargaHoraria, $dataConclusao, $unidade, $qrCodeUrl, $templatePath, $hash)
    {
        try {
            $certificadosDir = storage_path('app/certificados');
            $qrCodeDir = storage_path('app/qr_codes');
            if (!is_dir($certificadosDir)) {
                mkdir($certificadosDir, 0755, true);
            }
            if (!is_dir($qrCodeDir)) {
                mkdir($qrCodeDir, 0755, true);
            }

            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($qrCodeUrl)
                ->size(300)
                ->margin(10)
                ->build();

            $qrCodePath = $qrCodeDir . '/qrcode_' . uniqid() . '.png';
            file_put_contents($qrCodePath, $qrCode->getString());

            $pdf = new Fpdi();
            $pdf->AddPage('L');
            $pdf->setSourceFile($templatePath);
            $template = $pdf->importPage(1);
            $pdf->useTemplate($template);
            $pdf->SetFont('Arial', 'B', 32, true);
            $pdf->SetXY(3.38 * 10, 7.15 * 10);
            $pdf->Cell(22.94 * 10, 1.62 * 10, $nomeAluno, 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 15, true);
            Carbon::setLocale('pt_BR');
            $dataConclusao = Carbon::parse($dataConclusao);
            $dataFormatada = $dataConclusao->translatedFormat('j \d\e F \d\e Y');
            $pdf->SetXY(17.2, 89);
            $pdf->Cell(262.6, 24.2, "Participou do Curso de " . $curso . " realizado de forma presencial no dia " . $dataFormatada, 0, 1, 'C');
            $pdf->SetXY(17.2, 92);
            $pdf->SetXY(17.2, 98, $pdf->GetY());
            $pdf->Cell(262.6, 24.2, "na Faculdade Sao Leopoldo Mandic - " . $unidade, 0, 1, 'C');
            $pdf->Image($qrCodePath, 245, 160, 35, 35);

            /*   Coloca o hash abaixo do QR code
            $pdf->SetFont('Arial', 'I', 10); 
            $pdf->SetXY(245, 195);  
            $pdf->Cell(30, 10, 'Código de Validação: ' . $hash, 0, 1, 'C'); */

            $outputPath = "$certificadosDir/certificado-" . uniqid() . ".pdf";
            $pdf->Output('F', $outputPath);
            unlink($qrCodePath);
            return $outputPath;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function validarCertificado($hash)
    {
        $certificado = Certificado::where('hash', $hash)->first();
        if (!$certificado) {
            return response()->json(['erro' => 'Certificado não encontrado.'], 404);
        }
        return view('validar-certificado', [
            'certificado' => $certificado
        ]);
    }   

    public function download($hash)
    {
        $certificado = Certificado::where('hash', $hash)->firstOrFail();

        return Storage::disk('s3')->download($certificado->certificado_path);
    }

    

    public function lerColunasExcel(Request $request)
{
    // Verifique explicitamente por erros de validação
    $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:xlsx,xls|max:10240'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'mensagem' => $validator->errors()->first()
        ], 422);
    }

    try {
        // Force o tratamento como JSON
        $dados = LaravelExcel::toArray(new CertificadosImport, $request->file('file'));

        return response()->json([
            'status' => 'success',
            'colunas' => $dados[0][0] ?? []
        ]);

    } catch (\Exception $e) {
        Log::error("Erro ao processar Excel: " . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'mensagem' => 'Erro ao processar arquivo. Verifique o formato.'
        ], 500);
    }
}

public function previewCertificado(Request $request)
{
    try {
        $request->validate([
            'template' => 'required|string',
            'nome' => 'required|string',
            'curso' => 'required|string',
            'carga_horaria' => 'required|string',
            'data_conclusao' => 'required|string',
            'unidade' => 'required|string',
        ]);

        // Gera um hash fictício para preview
        $hash = 'preview-' . uniqid();
        
        $outputPath = $this->gerarCertificadoPdf(
            $request->nome,
            $request->curso,
            $request->carga_horaria,
            $request->data_conclusao,
            $request->unidade,
            url('/verificar_certificado/' . $hash),
            storage_path("app/templates/" . basename($request->template)),
            $hash
        );

        return response()->file($outputPath)->deleteFileAfterSend();

    } catch (\Exception $e) {
        Log::error("Erro no preview: " . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'mensagem' => 'Erro ao gerar pré-visualização'
        ], 500);
    }
}


}
