export default class ClientModel {
  constructor({ 
    id, 
    name,
    document,
    responsible,
    phone,
    email,
    logo,
  }) {
    this.id          = id;
    this.name        = name;
    this.document    = document;
    this.responsible = responsible;
    this.phone       = phone;
    this.email       = email;
    this.logo        = logo;
  }
}