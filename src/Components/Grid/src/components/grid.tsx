import React, { ReactNode } from 'react'
import styled, { css } from 'styled-components'
import { device, breakPoints } from '~components/Style/src/components/devices'
import { GUTTER } from '~components/Config/src'

interface IBreakPoints {
  mobileSm?: [number, number]
  mobileMd?: [number, number]
  mobileLg?: [number, number]
  tablet?: [number, number]
  desktopSm?: [number, number]
  desktopMd?: [number, number]
  desktopLg?: [number, number]
}

export interface IGrid extends IBreakPoints {
  children: ReactNode
}

const createCss = (types: IBreakPoints) => {
  const style = Object.entries(types).map(([id, sizes]) => {
    if (!breakPoints[id]) {
      return ''
    }

    if (!sizes || !sizes.length) {
      return ''
    }

    const [span, col] = sizes

    return css`
      @media ${device(id)} {
        flex: ${() => {
          // wtf safari flex bug
          return /constructor/i.test((window as any).HTMLElement)
            ? `0 0 calc(${(span / col) * 100}% - 0.5px);`
            : `0 0 ${(span / col) * 100}%;`
        }};
      }
    `
  })

  return style
}

export interface IStyledGrid {
  types: IBreakPoints
}

export const StyledGrid = styled.div<IStyledGrid>`
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  flex: 0 0 100%;
  ${props => createCss(props.types)}
`

const Grid = ({
  mobileSm,
  mobileMd,
  mobileLg,
  tablet,
  desktopSm,
  desktopMd,
  desktopLg,
  children,
}: IGrid) => {
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

export default Grid
