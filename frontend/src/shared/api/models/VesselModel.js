export default class VesselModel {
  constructor({
    id,
    name,
    call_sign,
    port_of_registry,
    gross_tonnage,
    built_at,
    imo_number,
    active,
  }) {
    this.id               = id;
    this.name             = name;
    this.call_sign        = call_sign;
    this.port_of_registry = port_of_registry;
    this.gross_tonnage    = gross_tonnage;
    this.built_at         = built_at;
    this.imo_number       = imo_number;
    this.active           = active;
  }
}
