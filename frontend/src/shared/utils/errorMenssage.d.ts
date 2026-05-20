// src/shared/utils/errorMenssage.d.ts
import { getErrorMessage } from './shared/utils/errorMenssage.js';
declare module './errorMenssage.js' {
  export function getErrorMessage(errorResponse: any): string;
}
