// utils/cookie.ts

/**
 * Set a cookie with the specified name, value, and expiration days.
 *
 * @param name - The name of the cookie.
 * @param value - The value of the cookie.
 * @param days - The number of days the cookie should last. Default is 7 days.
 */
export function setCookie(name: string, value: string, days: number = 7): void {
    if (typeof document !== 'undefined') {
      const date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      const expires = `; expires=${date.toUTCString()}`;
      document.cookie = `${name}=${value || ""}${expires}; path=/`;
    }
  }
  
  /**
   * Retrieve the value of a cookie with the specified name.
   *
   * @param name - The name of the cookie.
   * @returns - The value of the cookie, or null if not found.
   */
  export function getCookie(name: string): string | null {
    if (typeof document !== 'undefined') {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop()?.split(';').shift() || null;
    }
    return null;
  }
  
  /**
   * Remove a cookie with the specified name.
   *
   * @param name - The name of the cookie.
   */
  export function removeCookie(name: string): void {
    if (typeof document !== 'undefined') {
      document.cookie = `${name}=; Max-Age=0; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
    }
  }
  