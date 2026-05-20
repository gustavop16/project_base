import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class PlanningService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/planning`;
    return api.get<{ data: [] }>(url);
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/planning/${id}`;
     return api.get<{ data: {} }>(url);
   }
 
   create(data: []): Promise<AxiosResponse<{}>> {
     const url = '/planning';
     return api.post<{}>(url, data);
   }
 
   update(
     id: number,
     data: []
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/planning/${id}`;
     return api.put<{ data: {} }>(url, data);
   }
 
   delete(id: number): Promise<AxiosResponse<void>> {
     const url = `/planning/${id}`;
     return api.delete<void>(url);
   }

   updateStatus(
     id: number,
     data: []
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/planning/${id}/status`;
     return api.put<{ data: {} }>(url, data);
   }
}

export default new PlanningService();
