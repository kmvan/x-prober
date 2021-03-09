import { BootstrapStore } from '@/Bootstrap/src/stores'
import fetch from 'isomorphic-unfetch'
interface serverFetchProps {
  data?: any
  status: number
}
export const serverFetch = (
  action: string,
  opts = {}
): Promise<serverFetchProps> => {
  return new Promise(async (resolve, reject) => {
    opts = {
      ...{
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Authorization: BootstrapStore.conf?.authorization,
        },
        cache: 'no-cache',
        credentials: 'omit',
      },
      ...opts,
    }
    const url = `${location.pathname}?action=${action}`
    const res = await fetch(url, opts)
    try {
      resolve({ status: res.status, data: await res.json() })
    } catch (e) {
      resolve({ status: res.status })
    }
  })
}
