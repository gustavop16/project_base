import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class PlaceService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/place`;
    return api.get<{ data: [] }>(url);
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/place/${id}`;
     return api.get<{ data: {} }>(url);
   }

  getByClient(client_id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/place/${client_id}/client`;
     return api.get<{ data: {} }>(url);
   }
 
   create(data: []): Promise<AxiosResponse<{}>> {
     const url = '/place';
     return api.post<{}>(url, data);
   }
 
   update(
     id: number,
     data: []
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/place/${id}`;
     return api.put<{ data: {} }>(url, data);
   }
 
   delete(id: number): Promise<AxiosResponse<void>> {
     const url = `/place/${id}`;
     return api.delete<void>(url);
   }
}

export default new PlaceService();
