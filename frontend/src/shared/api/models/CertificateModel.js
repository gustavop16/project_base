const STATUS_LABELS = {
  pending:   'Pendente',
  completed: 'Aguardando aprovação',
  approved:  'Aprovado',
  rejected:  'Reprovado',
}

export default class CertificateModel {
  constructor({
    id,
    token,
    status,
    monitoring_start,
    monitoring_end,
    form,
    vessel,
    created_by,
    approved_by,
    approved_at,
    rejected_reason,
    rejected_by,
    rejected_at,
    pdf_url,
    created_at,
  }) {
    this.id               = id;
    this.token            = token;
    this.status           = status;
    this.monitoring_start = monitoring_start ?? null;
    this.monitoring_end   = monitoring_end   ?? null;
    this.status_label    = STATUS_LABELS[status] ?? status;
    this.form            = form            ?? null;
    this.form_name       = form?.name      ?? '—';
    this.vessel          = vessel          ?? null;
    this.vessel_name     = vessel?.name    ?? '—';
    this.created_by      = created_by      ?? null;
    this.approved_by     = approved_by     ?? null;
    this.approved_at     = approved_at     ?? null;
    this.rejected_reason = rejected_reason ?? null;
    this.rejected_by     = rejected_by     ?? null;
    this.rejected_at     = rejected_at     ?? null;
    this.pdf_url         = pdf_url         ?? null;
    this.created_at      = created_at;
  }
}
