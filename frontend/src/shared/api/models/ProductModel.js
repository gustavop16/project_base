export default class ProductModel {
  constructor({
    id,
    name,
    description,
    unit,
    manufacturer,
    certification,
    classification,
    technical_standard,
    sales_cost,
    photo
  }) {
    this.id   = id;
    this.name = name;
    this.description = description;
    this.unit = unit;
    this.manufacturer = manufacturer;
    this.certification = certification;
    this.classification = classification;
    this.technical_standard = technical_standard;
    this.sales_cost = sales_cost;
    this.photo = photo;
  }
}