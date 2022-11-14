import { FC, ReactNode } from 'react'
import { createPortal } from 'react-dom'
import { usePortal } from '../../Hooks/use-portal'
export const Portal: FC<{ children: ReactNode }> = ({ children }) => {
  const target = usePortal()
  return createPortal(children, target)
}
