export const ready = (fn: () => void): void => {
  const ua: string = navigator.userAgent
  const eventName: string = 'attachEvent'
  switch (true) {
    case ua.indexOf('MSIE 8.0') > 0:
      window[eventName]('onreadystatechange', () => {
        if (document.readyState === 'complete') {
          fn()
        }
      })
      break
    case ua.indexOf('MSIE 9.0') > 0:
    case ua.indexOf('MSIE 10.0') > 0:
      window[eventName]('onreadystatechange', () => {
        if (document.readyState !== 'loading') {
          fn()
        }
      })
      break
    default:
      if (
        window[eventName]
          ? document.readyState === 'complete'
          : document.readyState !== 'loading'
      ) {
        fn()
      } else {
        document.addEventListener('DOMContentLoaded', fn)
      }
  }
}
