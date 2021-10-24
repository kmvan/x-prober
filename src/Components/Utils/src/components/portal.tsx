import { FC, ReactNode } from 'react'
import ReactDOM from 'react-dom'
import { usePortal } from '../../../Hooks/src/use-portal'
interface PortalProps {
  children: ReactNode
}
export const Portal: FC<PortalProps> = ({ children }) => {
  const target = usePortal()
  return ReactDOM.createPortal(children, target)
}
