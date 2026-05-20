import type { AxiosResponse } from 'axios';
import { api } from './api.service';
//import type { AttachmentModel } from '../models/attachment.model';

class AttachmentService {

   create(data: {}): Promise<AxiosResponse<{}>> {
      const url = '/attachments';
      return api.post<{}>(url, data,
        {
          headers: { 'Content-Type': 'multipart/form-data' }
        }
      );
    }

    delete(id: number): Promise<AxiosResponse<void>> {
      const url = `/attachments/${id}`;
      return api.delete<void>(url);
    }

    getAll(model:string, item_id: number): Promise<AxiosResponse<{ data: [] }>> {
      const url = `/attachments/${model}/${item_id}`;
      return api.get<{ data: [] }>(url);
    }
       
    downloadFile(path: String): Promise<AxiosResponse<Blob>> {
      const url = `/attachments/download/${path}`;
      return api.get<Blob>(url, { responseType: 'blob' });
    }
      
}

export default new AttachmentService();
