import BootstrapStore from '~components/Bootstrap/src/stores'

const restfulFetch = (action: string, opts = {}) => {
  return new Promise(async (resolve, reject) => {
    opts = {
      ...{
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Authorization: BootstrapStore.conf.authorization,
        },
        cache: 'no-cache',
        credentials: 'omit',
      },
      ...opts,
    }

    const url = `${location.pathname}?action=${action}`
    const res = await fetch(url, opts)
    const text = await res.text()

    if (!text.length) {
      resolve([res, {}])
    }

    try {
      resolve([res, JSON.parse(text)])
    } catch (e) {
      reject([res, {}])
    }
  }) as any
}

export default restfulFetch
