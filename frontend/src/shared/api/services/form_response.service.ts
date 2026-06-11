import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class FormResponseService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    return api.get<{ data: [] }>('/form-responses');
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/form-responses/${id}`);
  }

  create(data: {}): Promise<AxiosResponse<{}>> {
    return api.post<{}>('/form-responses', data);
  }
}

export default new FormResponseService();
