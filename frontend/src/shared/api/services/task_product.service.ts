import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class TaskProductService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/task-product`;
    return api.get<{ data: [] }>(url);
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/task-product/${id}`;
     return api.get<{ data: {} }>(url);
   }

   getByTask(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/task-product/${id}/task`;
     return api.get<{ data: {} }>(url);
   }

 
   create(data: []): Promise<AxiosResponse<{}>> {
     const url = '/task-product';
     return api.post<{}>(url, data);
   }
 
   update(
     id: number,
     data: []
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/task-product/${id}`;
     return api.put<{ data: {} }>(url, data);
   }
 
   delete(id: number): Promise<AxiosResponse<void>> {
     const url = `/task-product/${id}`;
     return api.delete<void>(url);
   }
}

export default new TaskProductService();
