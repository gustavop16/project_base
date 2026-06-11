
export default class UserModel {
  constructor({
    id,
    name,
    email,
    phone,
    type,
    observations,
    vessels,
  }) {
    this.id            = id;
    this.name          = name;
    this.email         = email;
    this.phone         = phone        ?? "";
    this.type          = type         ?? "";
    this.observations  = observations ?? "";
    this.vessels       = vessels      ?? [];
    this.vessel_ids    = (vessels     ?? []).map(v => v.id);
    this.vessels_names = (vessels     ?? []).map(v => v.name).join(', ') || '—';
  }
}
