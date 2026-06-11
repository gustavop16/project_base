export default class CertificateFormModel {
  constructor({
    id,
    name,
    description,
    active,
    questions_count,
    questions,
  }) {
    this.id                = id;
    this.name              = name;
    this.description       = description;
    this.description_short = description
      ? (description.length > 80 ? description.slice(0, 80) + '…' : description)
      : '';
    this.active            = active;
    this.questions_count   = questions_count ?? (questions?.length ?? 0);
    this.questions         = questions ?? [];
  }
}
