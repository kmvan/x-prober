import queryString from 'query-string'

interface IHeader {
  body?: any
  method?: string
  [key: string]: any
}

interface IUrlArgs {
  [key: string]: string
}

interface IResults {
  code?: number
  msg?: string
  data?: any
}

export default (urlArgs: IUrlArgs, header: IHeader = {}) => {
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

    let res: IResults = {}

    try {
      const resource = await fetch('?' + queryString.stringify(urlArgs), header)
      res = await resource.json()
    } finally {
      resolve(res)
    }
  }) as IResults | undefined
}
