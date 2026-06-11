import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class VesselService {

  getAll(all = false): Promise<AxiosResponse<{ data: [] }>> {
    return api.get<{ data: [] }>('/vessels', { params: all ? { all: 1 } : {} });
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
    const url = `/vessels/${id}`;
    return api.get<{ data: {} }>(url);
  }

  create(data: {}): Promise<AxiosResponse<{}>> {
    const url = '/vessels';
    return api.post<{}>(url, data);
  }

  update(id: number, data: {}): Promise<AxiosResponse<{ data: {} }>> {
    const url = `/vessels/${id}`;
    return api.post<{ data: {} }>(url, data);
  }

  delete(id: number): Promise<AxiosResponse<void>> {
    const url = `/vessels/${id}`;
    return api.delete<void>(url);
  }
}

export default new VesselService();
