export default class HistoryPlanningModel {
  constructor({ 
    id, 
    client,
    task,
    place,
    status,
    responsible_name,
    executing_company,
    execution_date,
    execution_date_br,
    observations,
    certification
  }) {
    this.id                 = id;
    this.client           = client;
    this.task           = task;
    this.place           = place;
    this.status             = status;
    this.responsible_name   = responsible_name;
    this.executing_company  = executing_company;
    this.execution_date     = execution_date;
    this.execution_date_br  = execution_date_br;
    this.observations       = observations;
    this.certification      = certification;
  }
}