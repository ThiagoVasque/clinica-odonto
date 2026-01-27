<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #22d3ee; margin-bottom: 30px; padding-bottom: 10px; }
        .clinic-name { font-size: 22px; font-weight: bold; text-transform: uppercase; }
        .content { margin: 20px 0; min-height: 300px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; }
        .signature { margin-top: 30px; border-top: 1px solid #999; display: inline-block; width: 300px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="clinic-name">JR ODONTOLOGIA</div>
        <p>Documento Clínico Digital</p>
    </div>

    <div class="content">
        {!! $documento->conteudo !!}
    </div>

    <div class="footer">
        <div class="signature">
            <p>Assinatura do Profissional Responsável</p>
        </div>
        <p>Emitido em: {{ $documento->created_at->format('d/m/Y \à\s H:i') }}</p>
    </div>
</body>
</html>