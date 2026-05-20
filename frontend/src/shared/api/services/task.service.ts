import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class TaskService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/task`;
    return api.get<{ data: [] }>(url);
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/task/${id}`;
     return api.get<{ data: {} }>(url);
   }

   getByType(type: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/task/${type}/type`;
     return api.get<{ data: {} }>(url);
   }

 
   create(data: []): Promise<AxiosResponse<{}>> {
     const url = '/task';
     return api.post<{}>(url, data,
        {
          headers: { 'Content-Type': 'multipart/form-data' }
        }
     );
   }
 
   update(
     id: number,
     data: {}
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/task/${id}`;
     return api.post<{ data: {} }>(url, data);
   }
 
   delete(id: number): Promise<AxiosResponse<void>> {
     const url = `/task/${id}`;
     return api.delete<void>(url);
   }
}

export default new TaskService();
