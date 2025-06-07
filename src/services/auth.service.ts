import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject, tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private baseUrl = 'http://localhost/php';

  // Comportamiento inicial del usuario (null si no hay)
  private userSubject = new BehaviorSubject<any>(null);
  user$ = this.userSubject.asObservable();

  constructor(private http: HttpClient) {
    // Al iniciar, cargar usuario de localStorage si existe
    const user = localStorage.getItem('user');
    if (user) {
      this.userSubject.next(JSON.parse(user));
    }
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post(`${this.baseUrl}/login.php`, { email, password }).pipe(
      tap((res: any) => {
        if (res.success && res.user) {
          // Guardar usuario en localStorage y actualizar BehaviorSubject
          localStorage.setItem('user', JSON.stringify(res.user));
          this.userSubject.next(res.user);
        }
      })
    );
  }

  register(nombre: string, email: string, password: string): Observable<any> {
    return this.http.post(`${this.baseUrl}/registro.php`, { nombre, email, password });
  }

  logout(): void {
    localStorage.removeItem('user');
    this.userSubject.next(null);
  }

  get currentUser() {
    return this.userSubject.value;
  }

  isLoggedIn(): boolean {
    return !!this.userSubject.value;
  }

setUser(user: any) {
  localStorage.setItem('user', JSON.stringify(user));
  this.userSubject.next(user);
}


}
