import { FC, HTMLAttributes } from 'react'
import styles from './styles.module.scss'
export const CardError: FC<HTMLAttributes<HTMLDivElement>> = (props) => (
  <div className={styles.error} {...props} />
)
