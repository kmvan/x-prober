import { FC, HTMLProps } from 'react'
import styles from './styles.module.scss'
export const MultiItemContainer: FC<HTMLProps<HTMLDivElement>> = (props) => (
  <div className={styles.multiItemContainer} {...props} />
)
