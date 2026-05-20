export default class TaskModel {
  constructor({
    id,
    description,
    interval_days,
    photo
  }) {
    this.id   = id;
    this.description = description;
    this.interval_days = interval_days;
    this.photo = photo;
  }
}