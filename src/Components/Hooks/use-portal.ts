import { useEffect, useRef } from 'react'
export const usePortal = (id?: string): HTMLDivElement => {
  const rootElemRef = useRef<HTMLDivElement>(document.createElement('div'))
  useEffect(() => {
    if (id) {
      const parentElem = document.getElementById(id)
      if (!parentElem) {
        return
      }
      parentElem.innerHTML = ''
      parentElem.appendChild(rootElemRef.current)
    } else {
      document.body.appendChild(rootElemRef.current)
    }
    // eslint-disable-next-line consistent-return
    return () => {
      // eslint-disable-next-line react-hooks/exhaustive-deps
      rootElemRef.current.remove()
    }
  }, [id])
  return rootElemRef.current
}
