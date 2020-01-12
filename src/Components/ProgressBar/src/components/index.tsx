import React from 'react'
import styled from 'styled-components'
import formatBytes from '~components/Helper/src/components/format-bytes'
import gradientColors from '~components/Helper/src/components/gradient'
import { GUTTER, COLOR_DARK } from '~components/Config/src'
import { rgba } from 'polished'

export interface IProgressBar {
  title?: string
  value: number
  max: number
  isCapacity: boolean
  percentTag?: string
}

const StyledProgressBar = styled.div`
  position: relative;
`

const StyledProgressOverview = styled.div`
  position: absolute;
  right: ${GUTTER};
  bottom: 0;
  z-index: 1;
  font-weight: 700;
  color: #fff;
  text-shadow: 0 1px 1px #000;
  line-height: 2rem;
  font-family: 'Arial Black';
`
const StyledProgressPercent = styled(StyledProgressOverview)`
  left: ${GUTTER};
  right: auto;
`

const StyledProgressShell = styled.div`
  position: relative;
  display: flex;
  width: 100%;
  height: 2rem;
  background: ${COLOR_DARK};
  border-radius: ${GUTTER};
  box-shadow: inset 0 10px 20px ${rgba(COLOR_DARK, 0.3)};
  padding: 0.3rem;
`

interface IStyledProgressValue {
  percent: number
}

const StyledProgressValue = styled.div.attrs(
  ({ percent }: IStyledProgressValue) => ({
    style: {
      backgroundColor: `#${
        gradientColors('#00cc00', '#ef2d2d')[~~percent - 1]
      }`,
      width: `${percent}%`,
    },
  })
)<IStyledProgressValue>`
  position: relative;
  transition: all 1s;
  border-radius: ${GUTTER};
  background: #0c0;
  box-shadow: inset 0 -5px 10px ${rgba(COLOR_DARK, 0.4)},
    0 5px 10px 0 ${rgba(COLOR_DARK, 0.3)};
  overflow: hidden;

  ::after,
  ::before {
    position: absolute;
    content: '';
    top: 0;
    height: 50%;
    width: 100%;
    border-radius: 0 0 50% 50%;
    background: linear-gradient(#fff, rgba(255, 255, 255, 0.3));
    opacity: 0.3;
  }
  ::before {
    background: linear-gradient(
      90deg,
      rgba(255, 255, 255, 0.1),
      rgba(255, 255, 255, 0.5),
      rgba(255, 255, 255, 0.1)
    );
    opacity: 1;
    height: 1px;
    border-radius: 0;
  }
`

const ProgressBar = ({
  title = '',
  value,
  max,
  isCapacity,
  percentTag = '%',
}: IProgressBar) => {
  const percent = max === 0 || value === 0 ? 0 : (value / max) * 100
  const overview = isCapacity
    ? `${formatBytes(value)} / ${formatBytes(max)}`
    : `${value.toFixed(1)}${percentTag} / ${max}${percentTag}`

  return (
    <StyledProgressBar title={title}>
      <StyledProgressPercent>{`${percent.toFixed(
        1
      )}${percentTag}`}</StyledProgressPercent>
      <StyledProgressOverview>{overview}</StyledProgressOverview>
      <StyledProgressShell>
        <StyledProgressValue percent={percent} />
      </StyledProgressShell>
    </StyledProgressBar>
  )
}

export default ProgressBar
