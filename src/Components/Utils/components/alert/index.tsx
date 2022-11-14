import { FC, ReactNode } from 'react'
import styles from './styles.module.scss'
export interface AlertProps {
  isSuccess: boolean
  msg?: ReactNode
}
export const Alert: FC<AlertProps> = ({ isSuccess, msg = '' }) => (
  <div
    className={styles.main}
    data-ok={isSuccess || undefined}
    data-error={!isSuccess || undefined}
    data-icon={!msg || undefined}
  >
    {msg}
  </div>
)
