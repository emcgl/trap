# Run by typing : R --vanilla --args expressionfile=~/location/ formula=FORMULA-SCALED-PREDICTOR-GENE_ID.txt outputtxt=OUTCOME-SCALED-PREDICTOR-GENE_ID.txt < SCALED-PREDICTOR-GENE.R

# print command line args

library(preprocessCore)
commandArgs(trailingOnly=TRUE)
options(expressions=30000)

# process command line arguments
for (e in commandArgs(trailingOnly=TRUE)) {
  ta = strsplit(e,"=",fixed=TRUE)
  if(!is.null(ta[[1]][2])) {
    assign(ta[[1]][1],ta[[1]][2])
  } else {
    assign(ta[[1]][1],TRUE)
  }
}


expressionfile		# input residuals file
formula			# prediction formula
outputtxt			# results file: association of delta age with phenotypes of interest


expression <- read.table(expressionfile, header = TRUE, row.names = 1, stringsAsFactors=FALSE)
dim(expression)

nPrbs <- dim(expression)[1]
nSamples <- dim(expression)[2]
nPrbs
nSamples


oexpression <- expression[order(rownames(expression)),]

#genes_needed <- read.table("GENEID.txt", header = FALSE, stringsAsFactors=FALSE)
genes_needed <- read.table("/trap/backend/GENEID.txt", header = FALSE, stringsAsFactors=FALSE)

k <- rownames(oexpression)
l <- genes_needed[,1]

genes.present <- genes_needed[l %in% k,]
genes.notpresent <- genes_needed[!l %in% k,]

ngenes.notpresent <- length(genes.notpresent)
missing.genes=matrix(0,ngenes.notpresent,nSamples)
rownames(missing.genes) = genes.notpresent
colnames(missing.genes) = colnames(oexpression)
all.genes <- rbind(oexpression,missing.genes)

m=order(row.names(all.genes))
all.genes = all.genes[m,]

#myGENES <- cbind(GENEID = rownames(all.genes), all.genes)
#rownames(myGENES) <- NULL
#write.table(myGENES, "FULL-GENE-RES-MATRIX-SCALED.txt", , quote=FALSE, row.names=F, sep="\t")

# Transpose Gene Expression Data
t.all.genes <- t(all.genes)
dim(t.all.genes)

nSamples <- dim(t.all.genes)[1]
nPrbs <- dim(t.all.genes)[2]
nSamples
nPrbs

results=matrix(NA,nSamples,1)
colnames(results) = c("TA")
rownames(results) = rownames(t.all.genes)

selection = as.data.frame(t.all.genes)

# Load the formula - calculated using the prediction algorithm
age.predicted <- matrix(NA,nSamples, 1)

myModel = readLines(formula)
myModel = eval(parse(text=paste(myModel)))
age.predicted = myModel

rownames(results) = rownames(t.all.genes)

predictor <- age.predicted
length(predictor)

mean(predictor)
sd(predictor)

results[,1] = predictor

myDF <- cbind(SAMPLEID = rownames(results), results)
rownames(myDF) <- NULL
write.table(myDF, outputtxt, quote=FALSE, row.names=F, sep="\t")

