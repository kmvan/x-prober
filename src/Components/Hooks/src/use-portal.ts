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
    return () => {
      rootElemRef.current.remove()
    }
  }, [id])
  return rootElemRef.current
}
