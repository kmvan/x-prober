import { BootstrapConstants } from '../Bootstrap/constants'
interface ServerFetchProps {
  data?: any
  status: number
}
export const serverFetch = async (
  action: string,
  opts = {}
): Promise<ServerFetchProps> => {
  opts = {
    ...{
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        Authorization: BootstrapConstants.conf?.authorization ?? '',
      },
      cache: 'no-cache',
      credentials: 'omit',
    },
    ...opts,
  }
  const url = `${window.location.pathname}?action=${action}`
  const res = await fetch(url, opts)
  try {
    return { status: res.status, data: await res.json() }
  } catch (e) {
    console.warn(e)
    return { status: res.status }
  }
}
