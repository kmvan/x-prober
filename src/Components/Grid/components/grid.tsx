import { FC, ReactNode } from 'react'
import styles from './styles.module.scss'
interface BreakPointsProps {
  xs?: number
  sm?: number
  md?: number
  lg?: number
  xl?: number
  xxl?: number
}
export interface GridProps extends BreakPointsProps {
  children: ReactNode
}
export interface StyledGridProps {
  types: BreakPointsProps
}
export const Grid: FC<GridProps> = ({ xs, sm, md, lg, xl, xxl, ...props }) => {
  const types = {
    xs,
    sm,
    md,
    lg,
    xl,
    xxl,
  }
  const data = {}
  for (const k of Object.keys(types)) {
    const v = types?.[k]
    if (!v) {
      continue
    }
    data[`data-${k}`] = v
  }
  return <div className={styles.grid} {...data} {...props} />
}
