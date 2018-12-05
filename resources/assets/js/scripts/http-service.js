import axios from 'axios';

export const http_auth = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  headers: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
    "Authorization": localStorage.getItem('token')
  },
})
export const http = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  headers: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
  },
})