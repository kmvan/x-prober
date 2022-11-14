import { FC, HTMLProps } from 'react'
import styles from './styles.module.scss'
export const CardDes: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.des} {...props} />
)
