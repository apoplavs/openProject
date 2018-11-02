import axios from 'axios';
// import { Observable } from 'rxjs';

// class HttpService {
//     constructor() {
//         this.http = axios.create({
//             baseURL: 'http://127.0.0.1:8000/api/v1'
//         })
//     }

//     _makeRequest(method, url, data = null, options = null) {
//         let request;
//         switch (method) {
//           case 'GET':
//             request = this.http.get(url, options);
//             break;
//           case 'POST':
//             request = this.http.post(url, data, options);
//             break;
//           case 'PUT':
//             request = this.http.put(url, data, options);
//             break;
//           case 'PATCH':
//             request = this.http.patch(url, data, options);
//             break;
//           case 'DELETE':
//             request = this.http.delete(url, options);
//             break;
//           default:
//             throw new Error('Method not supported');
//         }
        
//         return new Observable(subscriber => {
//             request.then((response) => {
//                 if (response.data['status'] in response.data) {
//                   if (response.data['status'] === 'error') {
//                     subscriber.error(new Error(response.data['error'], response.data['message']));
//                   } else  subscriber.next(response.data);
//                 } else subscriber.next(response.data);
//                 subscriber.complete();
//               }).catch((err) => {
//                 if (err.response) {
//                   if (err.response.data) {
//                     if (err.response.status) {
//                       subscriber.error(new Error(err.response.data.error, err.response.data.message));
//                     } else subscriber.error(err);
//                   } else subscriber.error(err);
//                 } else subscriber.error(err);
//                 subscriber.complete();
//               });
//         });
//       }

//     get(url, options) {
//         return this._makeRequest('GET', url, null, options = null);
//     }
    
//     post(url, data = null, options) {
//         return this._makeRequest('POST', url, data, options = null);
//     }
    
//     put(url, data = null, options) {
//         return this._makeRequest('PUT', url, data, options = null);
//      }
    
//     patch(url, data = null, options) {
//         return this._makeRequest('PATCH', url, data, options = null);
//     }
    
//     delete(url, options) {
//         return this._makeRequest('DELETE', url, null, options = null);
//     }
// }

// export default new HttpService();

export const HTTP = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  headers: {
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
    "Authorization": localStorage.getItem('token')
  },
})