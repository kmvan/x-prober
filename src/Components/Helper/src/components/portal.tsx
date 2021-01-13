import { createPortal } from 'react-dom'
import { useEffect, useState } from 'react'
const Portal = ({ children }) => {
  const [container] = useState(() => document.createElement('div'))
  useEffect(() => {
    document.body.appendChild(container)
    return () => {
      document.body.removeChild(container)
    }
  }, [])
  return createPortal(children, container)
}
export default Portal
