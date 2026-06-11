import type { AxiosResponse } from 'axios';
//import { UpdateUserDTO } from '../dtos';
//import type { UserModel } from '../models';
import { api } from './api.service';

class UserService {

  getAll(): Promise<AxiosResponse<{ data: [] }>> {
    const url = `/users`;
    return api.get<{ data: [] }>(url);
  }

  getById(id: number): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/users/${id}`;
     return api.get<{ data: {} }>(url);
   }
 
   create(data: []): Promise<AxiosResponse<{}>> {
     const url = '/users';
     return api.post<{}>(url, data,
        {
          headers: { 'Content-Type': 'multipart/form-data' }
        }
     );
   }
 
   getProfile(): Promise<AxiosResponse<{ data: {} }>> {
     return api.get<{ data: {} }>('/profile');
   }

   updateProfile(data: {}): Promise<AxiosResponse<{ data: {} }>> {
     return api.patch<{ data: {} }>('/profile', data);
   }

   update(
     id: number,
     data: {}
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/users/${id}`;
     return api.post<{ data: {} }>(url, data);
   }

   updatePassword(
     id: number,
     data: []
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/users/${id}/password`;
     return api.put<{ data: {} }>(url, data);
   }

   updatePhoto(
     id: number,
     data: {}
   ): Promise<AxiosResponse<{ data: {} }>> {
     const url = `/users/${id}/photo`;
     return api.post<{ data: {} }>(url, data,
      {
        headers: { 'Content-Type': 'multipart/form-data' }
      }
     );
   }
 
   delete(id: number): Promise<AxiosResponse<void>> {
     const url = `/users/${id}`;
     return api.delete<void>(url);
   }
/*
   createAttachment(id: number, data: []): Promise<AxiosResponse<{}>> {
     const url = `/users/${id}/attachment`;
     return api.post<{}>(url, data,
      {
        headers: { 'Content-Type': 'multipart/form-data' }
      }
     );
   }

   getAttachment(id: number): Promise<AxiosResponse<{ data: {} }>> {
    const url = `/users/${id}/attachment`;
    return api.get<{ data: {} }>(url);
  }
  */
}

export default new UserService();
