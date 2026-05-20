
export default class UserModel {
  constructor({
    id,
    name,
    email,
    phone,
    type,
    observations,
  }) {
    this.id           = id;
    this.name         = name;
    this.email        = email;
    this.phone        = phone        ?? "";
    this.type         = type         ?? "";
    this.observations = observations ?? "";
  }
}
