import queryString from 'query-string'

interface Iheader {
  body?: any
  method?: string
  [key: string]: any
}

interface IurlArgs {
  [key: string]: string
}

interface Iresults {
  code?: number
  msg?: string
  data?: any
}

export default (urlArgs: IurlArgs, header: Iheader = {}) => {
  return new Promise(async (resolve, reject) => {
    if (header.body) {
      if (!header.method) {
        header.method = 'post'
      }
    }

    header = {
      ...{
        credentials: 'same-origin',
        method: 'get',
      },
      ...header,
    }

    let res: Iresults = {}

    try {
      const resource = await fetch('?' + queryString.stringify(urlArgs), header)
      res = await resource.json()
    } finally {
      resolve(res)
    }
  }) as Iresults | undefined
}
