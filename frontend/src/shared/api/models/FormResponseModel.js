export default class FormResponseModel {
  constructor({
    id,
    status,
    observations,
    certificate_form,
    user,
    answers_count,
    answers,
    created_at,
  }) {
    this.id               = id;
    this.status           = status;
    this.observations     = observations;
    this.certificate_form = certificate_form ?? null;
    this.form_name        = certificate_form?.name ?? '—';
    this.vessel_name      = certificate_form?.vessel?.name ?? '—';
    this.user             = user ?? null;
    this.user_name        = user?.name ?? '—';
    this.answers_count    = answers_count ?? (answers?.length ?? 0);
    this.answers          = answers ?? [];
    this.created_at       = created_at;
  }
}
