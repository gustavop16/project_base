import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class CertificateService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    return api.get<{ data: [] }>('/certificates');
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/certificates/${id}`);
  }

  create(data: {}): Promise<AxiosResponse<{ data: {} }>> {
    return api.post<{ data: {} }>('/certificates', data);
  }

  delete(id: number): Promise<AxiosResponse<void>> {
    return api.delete<void>(`/certificates/${id}`);
  }

  getForm(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/certificates/${id}/form`);
  }

  getResponse(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/certificates/${id}/response`);
  }

  getHistory(id: number): Promise<AxiosResponse<{ data: [] }>> {
    return api.get<{ data: [] }>(`/certificates/${id}/history`);
  }

  submitResponse(id: number, data: {}): Promise<AxiosResponse<{}>> {
    return api.post<{}>(`/certificates/${id}/respond`, data);
  }

  approve(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.post<{ data: {} }>(`/certificates/${id}/approve`);
  }

  reject(id: number, reason: string): Promise<AxiosResponse<{ data: {} }>> {
    return api.post<{ data: {} }>(`/certificates/${id}/reject`, { reason });
  }

  downloadPdf(id: number): Promise<AxiosResponse<Blob>> {
    return api.get<Blob>(`/certificates/${id}/pdf`, { responseType: 'blob' });
  }

  verify(token: string): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/certificates/verify/${token}`);
  }
}

export default new CertificateService();
