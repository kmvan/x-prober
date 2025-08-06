import { BootstrapConstants } from '../Bootstrap/components/constants';

interface ServerFetchProps<T> {
  data: T | null;
  status: number;
}
const isDev = import.meta.env?.MODE === 'development';
export const serverFetchRoute = (action: string) => {
  return `${isDev ? '/api' : window.location.pathname}?action=${action}`;
};
export const serverFetch = async <T>(
  action: string,
  opts = {}
): Promise<ServerFetchProps<T>> => {
  const fetchOpts: RequestInit = {
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
  };
  const res = await fetch(serverFetchRoute(action), fetchOpts);
  return {
    status: res.status,
    data: res.ok ? await res.json().catch(() => null) : null,
  };
};
