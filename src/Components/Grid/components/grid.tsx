import { FC, ReactNode } from 'react'
import styled, { css } from 'styled-components'
import { GUTTER } from '../../Config'
import {
  breakPoints,
  device,
  DeviceIdProps,
} from '../../Style/components/devices'
interface BreakPointsProps {
  mobileSm?: [number, number]
  mobileMd?: [number, number]
  mobileLg?: [number, number]
  tablet?: [number, number]
  desktopSm?: [number, number]
  desktopMd?: [number, number]
  desktopLg?: [number, number]
}
export interface GridProps extends BreakPointsProps {
  children: ReactNode
}
const createCss = (types: BreakPointsProps) => {
  const style = Object.entries(types).map(([id, sizes]) => {
    if (!breakPoints[id]) {
      return ''
    }
    if (!sizes || !sizes.length) {
      return ''
    }
    const [span, col] = sizes
    return css`
      @media ${device(id as DeviceIdProps)} {
        flex: ${() =>
          // wtf safari flex bug
          /constructor/i.test((window as any).HTMLElement)
            ? `0 0 calc(${(span / col) * 100}% - 0.5px);`
            : `0 0 ${(span / col) * 100}%;`};
      }
    `
  })
  return style
}
export interface StyledGridProps {
  types: BreakPointsProps
}
export const StyledGrid = styled.div<StyledGridProps>`
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  flex: 0 0 100%;
  ${(props) => createCss(props.types)}
`
export const Grid: FC<GridProps> = ({
  mobileSm,
  mobileMd,
  mobileLg,
  tablet,
  desktopSm,
  desktopMd,
  desktopLg,
  children,
}) => {
  const types = {
    mobileSm,
    mobileMd,
    mobileLg,
    tablet,
    desktopSm,
    desktopMd,
    desktopLg,
  }
  return <StyledGrid types={types}>{children}</StyledGrid>
}
