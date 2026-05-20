import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class HistoryPlanningService {

  getAll(planning_id: number): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/history-planning/${planning_id}`;
    return api.get<{ data: [] }>(url);
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/history-planning/${id}`;
     return api.get<{ data: {} }>(url);
   }
 
}

export default new HistoryPlanningService();
