import { API } from './base'

export async function csrf() {
  await fetch(`${API}/sanctum/csrf-cookie`, {
    credentials: 'include',
  })
}
