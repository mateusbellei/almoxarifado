export function setStorageItem(key: string, value: string): void {
  if (typeof window !== 'undefined') {
    localStorage.setItem(key, value);
  }
}

export function getStorageItem(key: string): string | null {
  if (typeof window !== 'undefined') {
    return localStorage.getItem(key);
  }
  return null;
}

export function removeStorageItem(key: string): void {
  if (typeof window !== 'undefined') {
    localStorage.removeItem(key);
  }
}
