export default class PlanningModel {
  constructor({ 
    id, 
    client,
    place,
    task,
    status,
    responsible_name,
    executing_company,
    execution_date,
    execution_date_br,
    observations,
    last_execution_date,
    next_execution_date
  }) {
    this.id     = id;
    this.client = client;
    this.place  = place;
    this.task   = task;
    this.status = status;
    this.responsible_name   = responsible_name;
    this.executing_company  = executing_company;
    this.execution_date     = execution_date;
    this.execution_date_br  = execution_date_br;
    this.observations       = observations;
    this.last_execution_date = last_execution_date ?? '-';
    this.next_execution_date = next_execution_date ?? '-';
  }
}