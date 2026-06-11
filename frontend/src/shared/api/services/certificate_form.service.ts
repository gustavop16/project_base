import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class CertificateFormService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    return api.get<{ data: [] }>('/certificate-forms');
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/certificate-forms/${id}`);
  }

  create(data: {}): Promise<AxiosResponse<{}>> {
    return api.post<{}>('/certificate-forms', data);
  }

  update(id: number, data: {}): Promise<AxiosResponse<{ data: {} }>> {
    return api.post<{ data: {} }>(`/certificate-forms/${id}`, data);
  }

  delete(id: number): Promise<AxiosResponse<void>> {
    return api.delete<void>(`/certificate-forms/${id}`);
  }
}

export default new CertificateFormService();
