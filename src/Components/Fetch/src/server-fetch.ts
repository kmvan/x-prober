import fetch from 'isomorphic-unfetch'
import { BootstrapStore } from '../../Bootstrap/src/stores'

interface serverFetchProps {
  data?: any
  status: number
}
export const serverFetch = async (
  action: string,
  opts = {}
): Promise<serverFetchProps> => {
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
    return { status: res.status, data: await res.json() }
  } catch (e) {
    console.error(e)
    return { status: res.status }
  }
}
