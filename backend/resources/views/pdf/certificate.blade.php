<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 11px;
      color: #1a1a1a;
      background: #fff;
    }

    .page { padding: 36px 48px; }

    /* ── Header ── */
    .header {
      border-bottom: 3px solid #1e3a5f;
      padding-bottom: 14px;
      margin-bottom: 20px;
    }
    .header-table { width: 100%; }
    .header-app   { font-size: 13px; color: #4b6a8f; font-weight: bold; letter-spacing: 1px; }
    .header-id    { font-size: 10px; color: #888; margin-top: 2px; }
    .header-badge { text-align: right; vertical-align: top; }

    /* ── Form title block ── */
    .form-title {
      font-size: 18px;
      font-weight: bold;
      color: #1e3a5f;
      margin-bottom: 4px;
    }
    .form-description {
      font-size: 11px;
      color: #666;
      margin-bottom: 20px;
    }

    /* ── Section ── */
    .section { margin-bottom: 18px; }
    .section-title {
      font-size: 10px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #fff;
      background: #1e3a5f;
      padding: 5px 10px;
      margin-bottom: 8px;
    }
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table td {
      padding: 5px 8px;
      border-bottom: 1px solid #e8ecf0;
      vertical-align: top;
    }
    .info-table td.label {
      font-weight: bold;
      color: #4b6a8f;
      width: 40%;
      white-space: nowrap;
    }

    /* ── Answers ── */
    .answer-block {
      border: 1px solid #dce3ea;
      border-radius: 3px;
      margin-bottom: 8px;
      padding: 8px 10px;
    }
    .answer-question {
      font-weight: bold;
      color: #1e3a5f;
      margin-bottom: 4px;
    }
    .answer-value {
      color: #333;
      padding-left: 8px;
    }
    .answer-complement {
      font-style: italic;
      color: #666;
      padding-left: 8px;
      margin-top: 2px;
    }
    .no-answer { color: #aaa; font-style: italic; }

    /* ── Footer ── */
    .footer-table { width: 100%; margin-top: 24px; border-top: 2px solid #1e3a5f; padding-top: 14px; }
    .footer-approval { vertical-align: top; width: 60%; }
    .footer-qr { text-align: right; vertical-align: top; width: 40%; }
    .approval-label { font-size: 10px; color: #888; }
    .approval-value { font-size: 11px; font-weight: bold; color: #1e3a5f; }
    .qr-label { font-size: 9px; color: #aaa; margin-top: 4px; }

    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 10px;
      font-weight: bold;
      background: #d1fae5;
      color: #065f46;
    }
  </style>
</head>
<body>
<div class="page">

  {{-- ── HEADER ── --}}
  <div class="header">
    <table class="header-table">
      <tr>
        <td>
          <div class="header-app">{{ env('APP_NAME', 'OceanSafer') }}</div>
          <div class="header-id">Certificado Nº {{ str_pad($certificate->id, 6, '0', STR_PAD_LEFT) }}</div>
        </td>
        <td class="header-badge">
          <span class="badge">APROVADO</span><br/>
          <span style="font-size:9px; color:#888; display:block; margin-top:4px;">
            {{ $certificate->approved_at?->format('d/m/Y H:i') }}
          </span>
        </td>
      </tr>
    </table>
  </div>

  {{-- ── TÍTULO E DESCRIÇÃO DO FORMULÁRIO ── --}}
  <div class="form-title">{{ $certificate->certificateForm->name }}</div>
  @if($certificate->certificateForm->description)
  <div class="form-description">{{ $certificate->certificateForm->description }}</div>
  @endif

  {{-- ── NAVIO ── --}}
  <div class="section">
    <div class="section-title">Dados do Navio</div>
    <table class="info-table">
      <tr>
        <td class="label">Nome</td>
        <td>{{ $certificate->vessel->name }}</td>
        <td class="label">Número IMO</td>
        <td>{{ $certificate->vessel->imo_number ?? '—' }}</td>
      </tr>
      <tr>
        <td class="label">Número Distintivo</td>
        <td>{{ $certificate->vessel->call_sign ?? '—' }}</td>
        <td class="label">Porto de Registro</td>
        <td>{{ $certificate->vessel->port_of_registry ?? '—' }}</td>
      </tr>
      <tr>
        <td class="label">Tonelagem Bruta</td>
        <td>{{ $certificate->vessel->gross_tonnage ? number_format($certificate->vessel->gross_tonnage, 2, ',', '.') . ' t' : '—' }}</td>
        <td class="label">Data de Construção</td>
        <td>{{ $certificate->vessel->built_at?->format('d/m/Y') ?? '—' }}</td>
      </tr>
    </table>
  </div>

  {{-- ── RESPOSTAS ── --}}
  @if($response)
  <div class="section">
    <div class="section-title">Respostas do Formulário</div>

    @forelse($response->answers as $answer)
      @if($answer->question)
      <div class="answer-block">
        <div class="answer-question">
          {{ $loop->iteration }}. {{ $answer->question->question }}
        </div>
        @php
          $val = $answer->answer;
          $display = is_array($val)
            ? implode(', ', $val)
            : ($val ?? null);
        @endphp
        @if($display)
          <div class="answer-value">{{ $display }}</div>
        @else
          <div class="answer-value no-answer">Não respondido</div>
        @endif
        @if($answer->text_complement)
          <div class="answer-complement">Detalhe: {{ $answer->text_complement }}</div>
        @endif
      </div>
      @endif
    @empty
      <p style="color:#aaa; font-style:italic; padding: 8px;">Nenhuma resposta registrada.</p>
    @endforelse

    @if($response->observations)
    <div class="answer-block" style="background:#f8f9fa;">
      <div class="answer-question">Observações Gerais</div>
      <div class="answer-value">{{ $response->observations }}</div>
    </div>
    @endif
  </div>
  @endif

  {{-- ── FOOTER ── --}}
  <table class="footer-table">
    <tr>
      <td class="footer-approval">
        <p class="approval-label">Respondido por</p>
        <p class="approval-value">{{ $response?->user?->name ?? 'N/A' }}</p>

        <p class="approval-label" style="margin-top:10px;">Aprovado por</p>
        <p class="approval-value">{{ $certificate->approvedBy?->name ?? 'N/A' }}</p>

        <p class="approval-label" style="margin-top:10px;">Data de Aprovação</p>
        <p class="approval-value">{{ $certificate->approved_at?->format('d/m/Y \à\s H:i') ?? '—' }}</p>
      </td>
      <td class="footer-qr">
        <img src="{{ $qrCode }}" width="110" height="110" />
        <p class="qr-label">Escaneie para verificar<br/>a autenticidade</p>
      </td>
    </tr>
  </table>

</div>
</body>
</html>
