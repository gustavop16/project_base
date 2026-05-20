export default class TaskProductModel {
  constructor({
    id,
    task,
    product,
    amount
  }) {
    this.id   = id;
    this.task = task;
    this.product = product;
    this.amount = amount;
  }
}