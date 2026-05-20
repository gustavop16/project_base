import type { AxiosResponse } from 'axios';

import { api } from './api.service';

class authService {

  login(email: string, password: string): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/login`;
    return api.post<{ data: [] }>(url, {email: email, password: password});
  }

  logout(){
    localStorage.removeItem('token');
  }

  forgotPassword(email: string): Promise<AxiosResponse> {
    return api.post(`/forgot-password`, { email });
  }

  resetPassword(token: string | string[], email: string | string[], password: string, password_confirmation: string): Promise<AxiosResponse> {
    return api.post(`/reset-password`, { token, email, password, password_confirmation });
  }
}

export default new authService();
