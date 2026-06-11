import type { AxiosResponse } from 'axios';
import { api } from './api.service';

class QuestionService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    return api.get<{ data: [] }>('/questions');
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
    return api.get<{ data: {} }>(`/questions/${id}`);
  }

  create(data: {}): Promise<AxiosResponse<{}>> {
    return api.post<{}>('/questions', data);
  }

  update(id: number, data: {}): Promise<AxiosResponse<{ data: {} }>> {
    return api.post<{ data: {} }>(`/questions/${id}`, data);
  }

  delete(id: number): Promise<AxiosResponse<void>> {
    return api.delete<void>(`/questions/${id}`);
  }
}

export default new QuestionService();
