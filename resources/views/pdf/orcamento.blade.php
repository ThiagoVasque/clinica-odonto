<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #22d3ee; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; text-align: left; padding: 10px; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; }
        .total { text-align: right; font-size: 20px; font-weight: bold; margin-top: 20px; color: #0891b2; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>JR ODONTOLOGIA</h1>
        <p>ORÇAMENTO DE TRATAMENTO</p>
    </div>

    <p><strong>Paciente:</strong> {{ $orcamento->paciente->nome }}</p>
    <p><strong>Data:</strong> {{ $orcamento->created_at->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Procedimento</th>
                <th>Qtd</th>
                <th>V. Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orcamento->itens as $item)
            <tr>
                <td>{{ $item->procedimento->descricao }}</td>
                <td>{{ $item->quantidade }}</td>
                <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($item->quantidade * $item->valor_unitario, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        TOTAL: R$ {{ number_format($orcamento->itens->sum(fn($i) => $i->quantidade * $i->valor_unitario), 2, ',', '.') }}
    </div>

    <div class="footer">
        <p>Este orçamento é válido por 15 dias.</p>
    </div>
</body>
</html>