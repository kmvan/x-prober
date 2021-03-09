import { GUTTER } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
const StyledContainer = styled.div`
  margin-left: auto;
  margin-right: auto;
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  @media ${device('desktopSm')} {
    padding-left: ${GUTTER};
    padding-right: ${GUTTER};
  }
`
interface ContainerProps extends HTMLAttributes<HTMLDivElement> {}
export const Container = (props: ContainerProps) => {
  return <StyledContainer {...props} />
}
